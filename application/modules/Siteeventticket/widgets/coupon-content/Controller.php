<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_Widget_CouponContentController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        //DON'T RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteeventticket_coupon')) {
            return $this->setNoRender();
        }

        $this->view->share_coupon = Zend_Controller_Front::getInstance()->getRequest()->getParam('share_coupon', $this->_getParam('share_coupon', null));
        $siteeventticketCouponContent = Zend_Registry::isRegistered('siteeventticketCouponContent') ? Zend_Registry::get('siteeventticketCouponContent') : null;
        if(empty($siteeventticketCouponContent)) {
          return $this->setNoRender();
        }

        $this->view->siteeventcoupon = $siteeventcoupon = Engine_Api::_()->core()->getSubject();

        $this->view->statistics = $this->_getParam('statistics', array("startdate", "enddate", "couponcode", 'discount', 'expire'));

        //GET VIEWER INFO
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $siteeventcoupon->event_id);

        $can_create_coupons = $siteevent->authorization()->isAllowed($viewer, 'edit');

        $this->view->can_create_coupons = ($can_create_coupons && Engine_Api::_()->authorization()->isAllowed('siteevent_event', $viewer, 'coupon_creation'));

        $this->view->eventProfileCouponTabId = Engine_Api::_()->siteeventticket()->getContentId(array('page_name' => 'siteevent_index_view', 'widget_name' => 'siteeventticket.event-profile-coupons'));

        //INCREMENT IN NUMBER OF VIEWS
        $owner = $siteeventcoupon->getOwner();
        if (!$owner->isSelf($viewer)) {
            $siteeventcoupon->view_count++;
        }
        $siteeventcoupon->save();

        $this->view->count_coupon = Engine_Api::_()->getDbtable('coupons', 'siteeventticket')->getEventCouponCount($siteeventcoupon->event_id);
    }

}
