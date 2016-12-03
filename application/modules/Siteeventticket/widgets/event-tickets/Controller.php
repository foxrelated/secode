<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Conroller.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_Widget_EventTicketsController extends Engine_Content_Widget_Abstract {

    //ACTION FOR SHOWING THE TICKETS
    public function indexAction() {

        //TICKET SETTING DISABLED FROM ADMIN SIDE
        $ticket = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.ticket.enabled', 1);
        if (empty($ticket)) {
            return $this->setNoRender();
        }

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return $this->setNoRender();
        }

        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

        $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');
        $request = Zend_Controller_Front::getInstance()->getRequest();
        if (isset($request->occurrence_id) && !empty($request->occurrence_id)) {
            $this->view->occurrence_id = $occurrence_id = $request->occurrence_id;
        } else {
            $this->view->occurrence_id = $occurrence_id = Zend_Registry::isRegistered('occurrence_id') ? Zend_Registry::get('occurrence_id') : null;
        }

        $this->view->event_id = $params['event_id'] = $siteevent->event_id;

        //IF TAX IS MANDATORY AND NOT SET THEN NOT RENDER WIDGET
        $siteeventticketEventTickets = Zend_Registry::isRegistered('siteeventticketEventTickets') ? Zend_Registry::get('siteeventticketEventTickets') : null;
        if(empty($siteeventticketEventTickets)) {
          return $this->setNoRender();
        }
        
        $taxMandatory = Engine_Api::_()->siteeventticket()->isTaxRateMandatoryMessage($params['event_id']);
        if ($taxMandatory) {
          return $this->setNoRender();
        }
        //END
        
//        //DISPLAY HIDDEN TICKETS TO OWNER,ADMIN,SUPER-ADMIN,MODERATORE,LEADER,HOST
//        $isLeader = $siteevent->getLeaderList()->get($viewer);
//        $host = $siteevent->getHost()->getType();
//        if ($host == 'user') {
//            $host_id = $siteevent->getHost()->getIdentity();
//        }
//
//
        $params['hiddenTickets'] = false;
//
//        if ($viewer_id && ($siteevent->isOwner($viewer) || ($viewer->level_id == 1) || $isLeader || ($host_id == $viewer_id))) {
//            $params['hiddenTickets'] = true;
//        }
//        //END
        
        //DISPLAY TICKETS IN ORDER SELECTED IN WIDGET SETTING
        $orderBy = $request->getParam('orderby', null);
        if (empty($orderBy)) {
            $params['orderby'] = $this->_getParam('orderby', 'ticket_id');
        }
        
        if(Engine_Api::_()->siteevent()->hasPackageEnable()){
         $package = Engine_Api::_()->getItem('siteeventpaid_package', $siteevent->package_id);
         if(!empty($package) && empty($package->ticket_type)) {
             $params['showOnlyFreeTickets'] = 1;
         }
        }        

        $params['columns'] = array('price', 'title', 'status');
        
        $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('tickets', 'siteeventticket')->getTicketsPaginator($params);
        $this->view->count = $paginator->getTotalItemCount();

        if (empty($this->view->count)) {
            return $this->setNoRender();
        }

        $this->view->showEventFullStatus = $this->_getParam('showEventFullStatus', 1);
        $this->view->showTicketStatus = $this->_getParam('showTicketStatus', 1);
        $this->view->isEventFull = $siteevent->isEventFull(array('occurrence_id' => $this->view->occurrence_id, 'checkWaitlistFlag' => 1));
        $this->view->waitlist_id = 0;
        if($this->view->showEventFullStatus && $this->view->isEventFull) {
            $params = array();
            $params['occurrence_id'] = $this->view->occurrence_id;
            $params['user_id'] = $viewer_id;
            $params['columnName'] = 'waitlist_id';
            $this->view->waitlist_id = Engine_Api::_()->getDbTable('waitlists', 'siteevent')->getColumnValue($params);            
        }        
    }

}
