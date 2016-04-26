<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupmember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: WidgetController.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroupmember_WidgetController extends Core_Controller_Action_Standard {

    public function requestMemberAction() {
        $this->view->notification = $this->_getParam('notification');
    }

    public function approveGroupAction() {
        $this->view->notification = $notification = $this->_getParam('notification');
        $this->view->member_id = '';
        if(isset($notification->params) && isset($notification->params['member_id'])) {
            $this->view->member_id = $notification->params['member_id'];
        }
    }

}