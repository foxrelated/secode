<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Dashboardmenus.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_Plugin_Dashboardmenus {

    private function _getEventId() {
        //TICKET SETTING ENABLED
        $ticket = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.ticket.enabled', 1);
        if (empty($ticket)) {
            return false;
        }
        //SITEEVENT OBJECT SET OR NOT
        $event_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('event_id', null);
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        if ($siteevent->getType() !== 'siteevent_event') {
            return false;
        }

        //EVENT LEVEL CHECK
        $viewer = Engine_Api::_()->user()->getViewer();
        $editPrivacy = $siteevent->authorization()->isAllowed($viewer, "edit");
        if (empty($editPrivacy)) {
            return false;
        }

        //ORDER PLACED FOR THIS EVENT
        $isEventOrderPlaced = Engine_Api::_()->getDbtable('orders', 'siteeventticket')->fetchRow(array('event_id = ?' => $event_id));
        if (!empty($isEventOrderPlaced)) {
            return $event_id;
        }

        //TICKET LEVEL CHECK
        $ticketAllow = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'siteevent_event', "ticket_create");
        if (empty($ticketAllow)) {
            return false;
        }

        return $event_id;
    }

    public function onMenuInitialize_SiteeventDashboardTickets($row) {

        $event_id = $this->_getEventId();
        if (empty($event_id)) {
            return false;
        }

        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        return array(
            'label' => 'Tickets',
            'route' => 'siteeventticket_ticket',
            'action' => 'manage',
            'class' => 'ajax_dashboard_enabled',
            'name' => 'siteevent_dashboard_paymentaccount',
            'params' => array(
                'event_id' => $siteevent->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SiteeventDashboardPaymentaccount($row) {

        $event_id = $this->_getEventId();
        if (empty($event_id)) {
            return false;
        }

        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        //PAYMENT FLOW CHECK
        $paymentToSiteadmin = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.payment.to.siteadmin', 0);
        if (empty($paymentToSiteadmin)) {
            return false;
        }

        return array(
            'label' => 'Payment Account',
            'route' => 'siteeventticket_order',
            'action' => 'payment-info',
            'class' => 'ajax_dashboard_enabled',
            'name' => 'siteevent_dashboard_paymentaccount',
            'params' => array(
                'event_id' => $siteevent->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SiteeventDashboardPaymentmethod($row) {

        $event_id = $this->_getEventId();
        if (empty($event_id)) {
            return false;
        }

        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        //PAYMENT FLOW CHECK
        $paymentToSiteadmin = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.payment.to.siteadmin', 0);
        if ($paymentToSiteadmin) {
            return false;
        }

        return array(
            'label' => 'Payment Methods',
            'route' => 'siteeventticket_order',
            'action' => 'payment-info',
            'class' => 'ajax_dashboard_enabled',
            'name' => 'siteevent_dashboard_paymentmethod',
            'params' => array(
                'event_id' => $siteevent->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SiteeventDashboardPaymentrequests($row) {

        $event_id = $this->_getEventId();
        if (empty($event_id)) {
            return false;
        }

        //PAYMENT FLOW CHECK
        $paymentToSiteadmin = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.payment.to.siteadmin', 0);
        if (empty($paymentToSiteadmin)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'siteeventticket_order',
            'action' => 'payment-to-me',
            'class' => 'ajax_dashboard_enabled',
            'name' => 'siteevent_dashboard_paymentrequests',
            'params' => array(
                'event_id' => $event_id
            ),
        );
    }

    public function onMenuInitialize_SiteeventDashboardYourbill($row) {

        $event_id = $this->_getEventId();
        if (empty($event_id)) {
            return false;
        }

        //PAYMENT FLOW CHECK
        $paymentToSiteadmin = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.payment.to.siteadmin', 0);
        if ($paymentToSiteadmin) {
            return false;
        }

        $commission = Engine_Api::_()->siteeventticket()->getOrderCommission($event_id);
        if (!empty($commission[1])) {
            return array(
                'label' => $row->label,
                'route' => 'siteeventticket_order',
                'action' => 'your-bill',
                'class' => 'ajax_dashboard_enabled',
                'name' => 'siteevent_dashboard_yourbill',
                'params' => array(
                    'event_id' => $event_id
                ),
            );
        } else
            return false;;
    }

    public function onMenuInitialize_SiteeventDashboardTransactions($row) {

        $event_id = $this->_getEventId();
        if (empty($event_id)) {
            return false;
        }

        //PAYMENT FLOW CHECK
        $paymentToSiteadmin = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.payment.to.siteadmin', 0);
        if ($paymentToSiteadmin) {
            $transactionActionName = 'transaction';
        } else {
            $transactionActionName = 'event-transaction';
        }

        return array(
            'label' => $row->label,
            'route' => 'siteeventticket_order',
            'action' => $transactionActionName,
            'class' => 'ajax_dashboard_enabled',
            'name' => 'siteevent_dashboard_transactions',
            'tab' => 54,
            'params' => array(
                'event_id' => $event_id
            ),
        );
    }

    public function onMenuInitialize_SiteeventDashboardManageorders($row) {

        $event_id = $this->_getEventId();
        if (empty($event_id)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'siteeventticket_order',
            'action' => 'manage',
            'class' => 'ajax_dashboard_enabled',
            'name' => 'siteevent_dashboard_manageorders',
            'actionName' => 'manage',
            'params' => array(
                'event_id' => $event_id
            ),
        );
    }

    public function onMenuInitialize_SiteeventDashboardSalesreports($row) {

        $event_id = $this->_getEventId();
        if (empty($event_id)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'siteeventticket_report_general',
            'action' => 'sales-statistics',
            'class' => 'ajax_dashboard_enabled',
            'name' => 'siteeventticket_order_sales-statistics',
            'tab' => 60,
            'params' => array(
                'event_id' => $event_id
            ),
        );
    }

    public function onMenuInitialize_SiteeventDashboardCoupons($row) {

        //IF SETTING DISABLED THEN DONT DISPLAY THIS TAB
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.ticket.enabled', 1)) {
            return false;
        }

        $event_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('event_id', null);
        if (empty($event_id)) {
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        $totalCoupons = Engine_Api::_()->getDbtable('coupons', 'siteeventticket')->getEventCouponCount($event_id);

        if (!Engine_Api::_()->authorization()->isAllowed('siteevent_event', $viewer, 'coupon_creation') && $totalCoupons <= 0) {
            return false;
        }

        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        if (!$siteevent->authorization()->isAllowed($viewer, 'edit')) {
            return false;
        }

        if (!Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'siteevent_event', "ticket_create")) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'siteeventticket_coupon',
            'action' => 'manage',
            'class' => 'ajax_dashboard_enabled',
            'name' => 'siteevent_dashboard_coupons',
            'tab' => 54,
            'params' => array(
                'event_id' => $event_id
            ),
        );
    }

    public function onMenuInitialize_SiteeventDashboardTermsofuse($row) {

        //IF SETTING DISABLED THEN DONT DISPLAY THIS TAB
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.ticket.enabled', 1)) {
            return false;
        }

        $event_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('event_id', null);
        if (empty($event_id)) {
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        if (!$siteevent->authorization()->isAllowed($viewer, 'edit')) {
            return false;
        }

        if (!Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'siteevent_event', "ticket_create")) {
            return false;
        }

        return array(
            'label' => 'Terms & Conditions',
            'route' => 'siteeventticket_ticket',
            'action' => 'terms-of-use',
            'class' => 'ajax_dashboard_enabled',
            'name' => 'siteevent_dashboard_termsofuse',
            'params' => array(
                'event_id' => $siteevent->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SiteeventDashboardTaxes($row) {
        $taxEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.tax.enabled', 0);
        if (empty($taxEnabled)) {
            return false;
        }

        $event_id = $this->_getEventId();
        if (empty($event_id)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'siteeventticket_tax_general',
            'action' => 'index',
            'class' => 'ajax_dashboard_enabled',
            'name' => 'siteeventticket_ticket_tax',
            'tab' => 60,
            'params' => array(
                'event_id' => $event_id
            ),
        );
    }

}
