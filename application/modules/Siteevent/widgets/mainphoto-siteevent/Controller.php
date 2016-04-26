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
class Siteevent_Widget_MainphotoSiteeventController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return $this->setNoRender();
        }

        //GET SUBJECT AND OTHER SETTINGS
        $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');

        $this->view->ownerName = $this->_getParam('ownerName', 0);
        $this->view->featuredLabel = $this->_getParam('featuredLabel', 1);
        $this->view->sponsoredLabel = $this->_getParam('sponsoredLabel', 1);

        //GET VIEWER AND CHECK VIEWER CAN EDIT PHOTO OR NOT
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->can_edit = $siteevent->authorization()->isAllowed($viewer, 'edit');
    }

}