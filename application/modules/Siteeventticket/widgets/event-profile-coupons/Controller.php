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
class Siteeventticket_Widget_EventProfileCouponsController extends Engine_Content_Widget_Abstract {

    protected $_childCount;

    public function indexAction() {

        //IF TICKET SETTING DISABLED FROM ADMIN SIDE
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.ticket.enabled', 1)) {
            return $this->setNoRender();
        }             
        
        //DON'T RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return $this->setNoRender();
        }

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.couponprivate', 0)) {
            return $this->setNoRender();
        }
        
        $siteeventticketEventProfileCoupons = Zend_Registry::isRegistered('siteeventticketEventProfileCoupons') ? Zend_Registry::get('siteeventticketEventProfileCoupons') : null;
        if(empty($siteeventticketEventProfileCoupons)) {
          return $this->setNoRender();
        }

        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

        $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');

        $can_create_coupons = $siteevent->authorization()->isAllowed($viewer, 'edit');

        $this->view->can_create_coupons = ($can_create_coupons && Engine_Api::_()->authorization()->isAllowed('siteevent_event', $viewer, 'coupon_creation'));

        $couponTable = Engine_Api::_()->getItemTable('siteeventticket_coupon');

        $params = array();
        $params['event_id'] = $siteevent->event_id;
        $params['show_all_coupons'] = $can_create_coupons;
        $params['var'] = 1;
        $this->view->paginator = $paginator = $couponTable->getSiteEventTicketCouponsPaginator($params);
        $this->view->paginator->setCurrentPageNumber($this->_getParam('page'));
        $this->view->paginator->setItemCountPerPage($this->_getParam('itemCount', 10));
        $this->_childCount = $this->view->paginator->getTotalItemCount();

        if (empty($can_create_coupons) && empty($this->_childCount)) {
            return $this->setNoRender();
        }

        $this->view->statistics = $this->_getParam('statistics', array("startdate", "enddate", "couponcode", 'discount', 'expire'));
        $this->view->truncation = $this->_getParam('truncation', 64);

        $params = $this->_getAllParams();
        $this->view->params = $params;
        if ($this->_getParam('loaded_by_ajax', false)) {
            $this->view->loaded_by_ajax = true;
            if ($this->_getParam('is_ajax_load', false)) {
                $this->view->is_ajax_load = true;
                $this->view->loaded_by_ajax = false;
                if (!$this->_getParam('onloadAdd', false))
                    $this->getElement()->removeDecorator('Title');
                $this->getElement()->removeDecorator('Container');
            } else {
                return;
            }
        }
        $this->view->showContent = true;
    }

    public function getChildCount() {
        return $this->_childCount;
    }

}
