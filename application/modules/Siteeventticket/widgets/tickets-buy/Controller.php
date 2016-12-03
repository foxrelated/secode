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
class Siteeventticket_Widget_TicketsBuyController extends Engine_Content_Widget_Abstract {

    //ACTION FOR SHOWING THE TICKETS
    public function indexAction() {

        //TICKET SETTING DISABLED FROM ADMIN SIDE
        $ticket = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.ticket.enabled', 1);
        if (empty($ticket)) {
            return $this->setNoRender();
        }

        //LOGGED IN OR NOT
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (!$viewer_id) {
            return $this->setNoRender();
        }

        //DON'T RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return $this->setNoRender();
        }
        
        $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');
        $params = $this->_getAllParams();
        
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $this->view->event_id = $params['event_id'] = $siteevent->event_id;

        if (empty($params['event_id'])) {
          return $this->setNoRender();
        }

        //IF TAX IS MANDATORY AND NOT SET THEN NOT RENDER WIDGET
        $taxEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.tax.enabled', 0);
        $taxMandatory = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.tax.mandatory', 0);
        $taxEventEnabled = Engine_Api::_()->getDbtable('otherinfo', 'siteevent')->getColumnValue($params['event_id'], 'is_tax_allow');
        $tax_rate = Engine_Api::_()->getDbtable('otherinfo', 'siteevent')->getColumnValue($params['event_id'], 'tax_rate');

        $siteeventticketTicketBuy = Zend_Registry::isRegistered('siteeventticketTicketBuy') ? Zend_Registry::get('siteeventticketTicketBuy') : null;
        if ((empty($siteeventticketTicketBuy)) || ($taxEnabled && $taxMandatory && (empty($tax_rate) || $tax_rate <= 0))) {
           return $this->setNoRender();
        }
        //END
        //APPLY TAX ON TICKET SUBTOTAL
        if ($taxEnabled && $taxEventEnabled && $tax_rate > 0) {
            $this->view->tax_rate = $tax_rate;
        }

        if ($this->_getParam('loaded_by_ajax', false)) {
            $this->getElement()->removeDecorator('Title');
            $this->getElement()->removeDecorator('Container');
        }

        $occurrence_id = $request->getParam('occurrence_id', null);

        $this->view->defaultoccurrence_id = $defaultoccurrence_id = $this->_getParam('defaultoccurrence_id', Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurrence());

        //DEFAULT OCCURRENCE FIRST TIME
        if ($occurrence_id) {
            $params['occurrence_id'] = $occurrence_id;
        } else {
            $params['occurrence_id'] = $defaultoccurrence_id;
        }

        $this->view->occurrence_id = $occurrence_id;
        $this->view->params = $params;
        $this->view->identity = $params['identity'] = $this->_getParam('identity', $this->view->identity);
        $this->view->datesInfo = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getAllOccurrenceDates($params['event_id'], 0, array('notIncludePastEvents' => 1));

        //DISPLAY HIDDEN TICKETS TO OWNER,ADMIN,SUPER-ADMIN,MODERATORE,LEADER,HOST
        $isLeader = $siteevent->getLeaderList()->get($viewer);
        $host = $siteevent->getHost();
        $host_id = 0;
        if (!empty($host) && $host->getType() == 'user') {
            $host_id = $siteevent->getHost()->getIdentity();
        }
        $params['hiddenTickets'] = false;
        if ($siteevent->isOwner($viewer) || ($viewer->level_id == 1) || $isLeader || ($host_id == $viewer_id)) {
            $params['hiddenTickets'] = true;
        }
        //END
        //CHECK THE COUPON SECTION VISIBILITY
        $this->view->showCouponSection = false;
        if (Engine_Api::_()->authorization()->isAllowed('siteevent_event', $siteevent->getOwner(), 'coupon_creation') && Engine_Api::_()->getDbTable('coupons', 'siteeventticket')->getEventCouponCount(array('event_id' => $siteevent->event_id))) {
            $this->view->showCouponSection = true;
        }

        //CHECK THE CAPACITY 
        $this->view->capacityApplicable = 0;
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.waitlist', 1) && !empty($siteevent->capacity)) {
            $this->view->capacityApplicable = 1;
        }

        //DISPLAY TICKETS IN ORDER SELECTED IN WIDGET SETTING
        $orderBy = $request->getParam('orderby', null);
        if (empty($orderBy)) {
            $params['orderby'] = $this->_getParam('orderby', 'ticket_id');
        }
        
        $params['columns'] = array('status','price', 'title', 'quantity', 'ticket_id', 'description', 'is_claimed_display', 'quantity', 'is_same_end_date', 'sell_endtime','sell_starttime', 'buy_limit_min', 'buy_limit_max');
        
        if(Engine_Api::_()->siteevent()->hasPackageEnable()){
         $package = Engine_Api::_()->getItem('siteeventpaid_package', $siteevent->package_id);
         if(!empty($package) && empty($package->ticket_type)) {
             $params['showOnlyFreeTickets'] = 1;
         }
        }
        
        $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('tickets', 'siteeventticket')->getTicketsPaginator($params);
        $this->view->count = $paginator->getTotalItemCount();
    }

}
