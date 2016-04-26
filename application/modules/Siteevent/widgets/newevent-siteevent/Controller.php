<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Widget_NeweventSiteeventController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
    $this->view->creation_link = $creation_link = Engine_Api::_()->authorization()->isAllowed('siteevent_event', $viewer, "creation_link");
    
    //DONT SHOW ADD LINK TO VISITOR
    if (empty($viewer_id) && !$creation_link) {
      return $this->setNoRender();
    }

    //CHECK EVENT CREATION PRIVACY
    if (!empty($viewer_id) && !Engine_Api::_()->authorization()->isAllowed('siteevent_event', $viewer, "create")) {
      return $this->setNoRender();
    }

    $parent_type = '';
    $parent_id = '';
    if (Engine_Api::_()->core()->hasSubject()) {
      $hasIntegrated = false;
      $subject = Engine_Api::_()->core()->getSubject();
      $moduleName = strtolower($subject->getModuleName());
      if ($moduleName == 'sitereview' && isset($subject->listingtype_id)) {
        if ((Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitereview_listing_' . $subject->listingtype_id, 'item_module' => 'sitereview'))))
          $hasIntegrated = true;
      } else {
        if ((Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => $subject->getType(), 'item_module' => strtolower($subject->getModuleName())))))
          $hasIntegrated = true;
      }

      if ($hasIntegrated) {
        $parent_type = $subject->getType();
        $parent_id = $subject->getIdentity();
        $isCreatePrivacy = Engine_Api::_()->siteevent()->isCreatePrivacy($parent_type, $parent_id);
        if (empty($isCreatePrivacy)) {
          return $this->setNoRender();
        }
      }
    }

    $this->view->parent_type = $parent_type;
    $this->view->parent_id = $parent_id;
    $this->view->quick = $this->_getParam('quick', 1);
  }

}