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
class Siteevent_Widget_ZeroeventSiteeventController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //CAN CREATE EVENTS OR NOT
        $this->view->can_create = Engine_Api::_()->authorization()->isAllowed('siteevent_event', $viewer, "create");

        //GET LISTS
        $eventCount = Engine_Api::_()->getDbTable('events', 'siteevent')->hasEvents();

        if ($eventCount > 0) {
            return $this->setNoRender();
        }
    }

}
