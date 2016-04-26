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
class Siteevent_Widget_LocationSidebarSiteeventController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        //DONT RENDER IF NOT AUTHORIZED
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return $this->setNoRender();
        }

        //GET SUBJECT
        $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1) || $siteevent->is_online) {
            return $this->setNoRender();
        }

        //GET LOCATION
        $value['id'] = $siteevent->getIdentity();

        $this->view->location = $location = Engine_Api::_()->getDbtable('locations', 'siteevent')->getLocation($value);

        //DONT RENDER IF LOCAITON IS EMPTY
        if (empty($location)) {
            return $this->setNoRender();
        }

        $this->view->showContent = $this->_getParam('showContent', array("startDate", "endDate"));
        $this->view->height = $this->_getParam('height', 200);
    }

}