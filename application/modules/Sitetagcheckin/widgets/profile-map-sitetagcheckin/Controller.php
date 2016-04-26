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
class Sitetagcheckin_Widget_ProfileMapSitetagcheckinController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //IF THERE IS NO SUBJECT THEN SET NO RENDER
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
    $location_id = 0;
    $this->view->location = $location = Engine_Api::_()->sitetagcheckin()->getCustomFieldLocation($subject);
    $sitetagcheckin_profilemap = Zend_Registry::isRegistered('sitetagcheckin_profilemap') ? Zend_Registry::get('sitetagcheckin_profilemap') : null;
    $this->view->checkin_show_options = $this->_getParam('checkin_show_options', 0);
    $this->view->checkin_map_height = $this->_getParam('checkin_map_height', 500);
    $resource_type = $subject->getType();

    if (empty($sitetagcheckin_profilemap) || (($resource_type == 'poll' || $resource_type == 'blog' || $resource_type == 'forum' || $resource_type == 'video' || $resource_type == 'music') && ($this->view->checkin_show_options == 1 || $this->view->checkin_show_options == 2))) {
      return $this->setNoRender();
    }

		if(empty($location) && ($this->view->checkin_show_options == 1 || $this->view->checkin_show_options == 2)) {
      return $this->setNoRender();
    }

//     if(empty($location)) {
//       $this->view->checkin_show_options == 0;
//     }

    if (!empty($location)) {
			$addLocationTable = Engine_Api::_()->getDbtable('addlocations', 'sitetagcheckin');
			$this->view->locationInformation = $addLocationTable->getLocationId($location, 1);
    }

  }

}
