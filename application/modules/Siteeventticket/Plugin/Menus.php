<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_Plugin_Menus {

    public function canCreateSiteeventtickets($row) {

        //MUST BE LOGGED IN USER
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer || !$viewer->getIdentity()) {
            return false;
        }

        //MUST BE ABLE TO VIEW TICKETS
        if (!Engine_Api::_()->authorization()->isAllowed('siteeventticket_ticket', $viewer, "view")) {
            return false;
        }

        //MUST BE ABLE TO CRETE TICKETS
        if (!Engine_Api::_()->authorization()->isAllowed('siteeventticket_ticket', $viewer, "create")) {
            return false;
        }

        return true;
    }

    public function canViewSiteeventtickets($row) {

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //MUST BE ABLE TO VIEW TICKETS
        if (!Engine_Api::_()->authorization()->isAllowed('siteeventticket_ticket', $viewer, "view")) {
            return false;
        }

        return true;
    }

    public function showAdminPaymentRequestTab() {
        $isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.payment.to.siteadmin', 0);
        if (empty($isPaymentToSiteEnable)) {
            return false;
        }
        return true;
    }

    public function showAdminCommissionTab() {
        $isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.payment.to.siteadmin', 0);
        if (empty($isPaymentToSiteEnable)) {
            return true;
        }
        return false;
    }

    public function canViewBrowseCoupons($row) {

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //MUST BE ABLE TO VIEW EVENTS
        if (!Engine_Api::_()->authorization()->isAllowed('siteevent_event', $viewer, "view") || Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.couponprivate', 0)) {
            return false;
        }

        return true;
    }

    public function siteeventticketMainMytickets() {

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //RETURN IF VIEWER IS EMPTY
        if (empty($viewer_id)) {
            return false;
        }

        //MUST BE ABLE TO VIEW EVENTS
        if (!Engine_Api::_()->authorization()->isAllowed('siteevent_event', $viewer, "view")) {
            return false;
        }

        if (!Engine_Api::_()->siteevent()->hasTicketEnable()) {
            return false;
        }
        return array(
            'route' => 'siteeventticket_order',
            'params' => array(
                'module' => 'siteeventticket',
                'controller' => 'order',
                'action' => 'my-tickets'
            ),
        );
    }
    
    public function ticketFaqs() {

        return array(
            'route' => 'admin_default',
            'module' => 'siteevent',
            'controller' => 'settings',
            'action' => 'faq',
            'params' => array(
                'faq_type' => 'tickets',
            ),
        );
    }    

    public function siteeventGutterMytickets() {

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        //RETURN IF VIEWER IS EMPTY
        if (empty($viewer_id)) {
            return false;
        }

        //MUST BE ABLE TO VIEW EVENTS
        if (!Engine_Api::_()->authorization()->isAllowed('siteevent_event', $viewer, "view")) {
            return false;
        }

        if (!Engine_Api::_()->siteevent()->hasTicketEnable()) {
            return false;
        }
        return array(
            'class' => 'icon_siteevents_tickets buttonlink',
            'route' => 'siteeventticket_order',
            'params' => array(
                'module' => 'siteeventticket',
                'controller' => 'order',
                'action' => 'my-tickets'
            ),
        );
    }
    
    public function onMenuInitialize_CoreMiniSiteeventticketmytickets($row)
    {
          //GET VIEWER
          $viewer = Engine_Api::_()->user()->getViewer();
          $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

          //RETURN IF VIEWER IS EMPTY
          if (empty($viewer_id)) {
              return false;
          }

          //MUST BE ABLE TO VIEW EVENTS
          if (!Engine_Api::_()->authorization()->isAllowed('siteevent_event', $viewer, "view")) {
              return false;
          }

          if (!Engine_Api::_()->siteevent()->hasTicketEnable()) {
              return false;
          }
          
          return array(
              //'class' => 'icon_siteevents_my_tickets',
              'route' => 'siteeventticket_order',
              'icon' => Zend_Registry::get('Zend_View')->layout()->staticBaseUrl."application/modules/Siteeventticket/externals/images/ticket.png",
              'params' => array(
                  'module' => 'siteeventticket',
                  'controller' => 'order',
                  'action' => 'my-tickets',
              ),
          );
    }      

}
