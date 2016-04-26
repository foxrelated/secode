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
class Sitetagcheckin_Widget_ProfileCheckinsSitetagcheckinController extends Engine_Content_Widget_Abstract {

  //ACTION FOR SHOWING THE CHECKIN FEEDS ON THE PROFILE PAGE
  public function indexAction() {

    //IF THERE IS NO SUBJECT THEN SET NO RENDER
    if (!Engine_Api::_()->core()->hasSubject('user')) {
      return $this->setNoRender();
    }
    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer(); 
    //GET SUBJECT AND CHECK AUTH
    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('user');
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }

    //GET USER SUBJECT
    $subject = Engine_Api::_()->core()->getSubject();

    //GET ADD LOCATION TABLE
    $addlocationsTable = Engine_Api::_()->getDbtable('addlocations', 'sitetagcheckin');

    //GET CHECKIN COUNT
    $this->view->check_in_count = $addlocationsTable->getCheckinCount($subject, null, null, 'checkin', 'parent_id');
    $sitetagcheckin_profile_check = Zend_Registry::isRegistered('sitetagcheckin_profile_check') ? Zend_Registry::get('sitetagcheckin_profile_check') : null;

    //IF THERE IS NO CHECKIN THEN SET NO RENDER
    if (empty($this->view->check_in_count) || empty($sitetagcheckin_profile_check)) {
      return $this->setNoRender();
    }

    //GET ZEND REQUEST
    $request = Zend_Controller_Front::getInstance()->getRequest();

    //GET LIMIT
    $limit = $request->getParam('limit', Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.length', 15));

    //GET UPDATE
    $this->view->getUpdate = $request->getParam('getUpdate');

    //GET UPDATE
    $this->view->noList = $request->getParam('noList');

    //SET PAGE NUMBER
    $page = $this->_getParam('page', 1);

    //GET ACTIONS
    $this->view->actions = $paginator = $addlocationsTable->getFeedItems($subject, "checkin", null, 4);

    //IF THERE IS NO CHECKIN THEN SET NO RENDER
    $count = 0;
    if($this->view->actions) {
        $count = $this->view->actions->getTotalItemCount();
    }
    if (empty($count)) {
      return $this->setNoRender();
    }

    //INITIALSING THE SHOW MAP VARIABLE
    $this->view->show_map = 2;

    //SET LIMIT ON PAGINATOR
    $paginator->setItemCountPerPage($limit)->setCurrentPageNumber($page);
  }

}

?>
