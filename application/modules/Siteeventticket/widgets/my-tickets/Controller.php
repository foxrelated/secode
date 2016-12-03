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
class Siteeventticket_Widget_MyTicketsController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {
        
        /*VALIDATIONS-START
        GET VIEWER DETAILS*/
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

        //RETURN IF VIEWER IS EMPTY
        if (empty($viewer_id)) {
            return $this->setNoRender();
        }

        //MUST BE ABLE TO VIEW EVENTS
        if (!Engine_Api::_()->authorization()->isAllowed('siteevent_event', $viewer, "view")) {
            return $this->setNoRender();
        }

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.ticket.enabled', 1)) {
            return $this->setNoRender();
        }
        //END
        $ajax = $this->_getParam('ajax', false);
        $this->view->quick = $this->_getParam('quick', 1);
        if ($this->_getParam('isajax', false) || $ajax) {
            $this->getElement()->removeDecorator('Title');
            $this->getElement()->removeDecorator('Container');
        }

        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation("siteevent_main");
        $this->view->call_same_action = $this->_getParam('call_same_action', 0);
        //GET SETTINGS
        $this->view->isajax = $this->_getParam('isajax', false);
        
        $siteeventticketMyTickets = Zend_Registry::isRegistered('siteeventticketMyTickets') ? Zend_Registry::get('siteeventticketMyTickets') : null;
        $this->view->showSendTicketLink = Engine_Api::_()->hasModuleBootstrap('sitemailtemplates') && file_exists('application/libraries/dompdf/dompdf_config.inc.php');        

        $params = array();
        $params['user_id'] = $viewer_id;
        $params['viewType'] = $this->view->viewType = $this->_getParam('viewType', 'current');
        $params['my_tickets_page'] = true;

        //MAKE PAGINATOR
        $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('orders', 'siteeventticket')->getOrdersPaginator($params);
        if ($params['viewType'] == 'current') {
            $this->view->totalUpcomingOrderCount = $paginator->getTotalItemCount();

            //CALCULATE PAST ORDERS COUNT
            $params['viewType'] = 'past';
            $this->view->totalPastOrderCount = Engine_Api::_()->getDbtable('orders', 'siteeventticket')->getOrdersPaginator($params)->getTotalItemCount();
        } else {
            $this->view->totalPastOrderCount = $paginator->getTotalItemCount();

            //CALCULATE CURRENT ORDERS COUNT
            $params['viewType'] = 'current';
            $this->view->totalUpcomingOrderCount = Engine_Api::_()->getDbtable('orders', 'siteeventticket')->getOrdersPaginator($params)->getTotalItemCount();
        }
        $this->view->total_item = $this->view->paginator->getTotalItemCount($this->view->paginator);

        $this->view->paginator->setItemCountPerPage($this->view->total_item);
        
        if(empty($siteeventticketMyTickets)) {
          return $this->setNoRender();
        }
    }

}
