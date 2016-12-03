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
class Siteeventticket_Widget_TicketStatisticsController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
      
        //IF TICKET SETTING DISABLED FROM ADMIN SIDE
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.ticket.enabled', 1)) {
            return $this->setNoRender();
        }
        
        $request = Zend_Controller_Front::getInstance()->getRequest();

        $params = array();
        $params['event_id'] = $event_id = $request->getParam('event_id', null);

        if (empty($event_id)) {
            return $this->setNoRender();
        }

        //VALIDATIONS
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$siteevent->authorization()->isAllowed($viewer, 'edit')) {
            return $this->setNoRender();
        }
        //END

        $orderTable = Engine_Api::_()->getDbtable('orders', 'siteeventticket');
        $this->view->event_statistics = $orderTable->getEventStatistics($params);
        //DON'T RENDER IF NO DATA
        if (Count($this->view->event_statistics) <= 0) {
            return $this->setNoRender();
        }
        
        $siteeventticketEventStatistics = Zend_Registry::isRegistered('siteeventticketEventStatistics') ? Zend_Registry::get('siteeventticketEventStatistics') : null;
        if(empty($siteeventticketEventStatistics))  {
          return $this->setNoRender();
        }
        
        $this->view->approval_pending_orders = $orderTable->getStatusOrders(array('event_id' => $event_id, 'order_status' => 0));
        $this->view->payment_pending_orders = $orderTable->getStatusOrders(array('event_id' => $event_id, 'order_status' => 1));
        $this->view->complete_orders = $orderTable->getStatusOrders(array('event_id' => $event_id, 'order_status' => 2));
        $this->view->total_orders = $orderTable->getStatusOrders(array('event_id' => $event_id));


        $this->view->currencySymbol = $currencySymbol = Zend_Registry::isRegistered('siteeventticket.currency.symbol') ? Zend_Registry::get('siteeventticket.currency.symbol') : null;
        if (empty($currencySymbol)) {
            $this->view->currencySymbol = Engine_Api::_()->siteeventticket()->getCurrencySymbol();
        }
    }

}
