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
class Siteeventticket_Widget_LatestOrdersController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        $request = Zend_Controller_Front::getInstance()->getRequest();

        $params = array();
        $this->view->eventId = $params['event_id'] = $request->getParam('event_id', null);

        //VALIDATIONS
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $params['event_id']);
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer->getIdentity();

        if (!$siteevent->authorization()->isAllowed($viewer, 'edit')) {
            return $this->setNoRender();
        }
        //END

        $params['limit'] = $this->_getParam('itemCount', 5);

        $this->view->latestOrders = Engine_Api::_()->getDbTable('orders', 'siteeventticket')->getLatestOrders($params);
        $siteeventticketLatestOrders = Zend_Registry::isRegistered('siteeventticketLatestOrders') ? Zend_Registry::get('siteeventticketLatestOrders') : null;
        if(empty($siteeventticketLatestOrders)) {
          return $this->setNoRender();
        }

        $this->view->currency_symbol = Zend_Registry::isRegistered('siteeventticket.currency.symbol') ? Zend_Registry::get('siteeventticket.currency.symbol') : null;
        if (empty($this->view->currency_symbol)) {
            $this->view->currency_symbol = Engine_Api::_()->siteeventticket()->getCurrencySymbol();
        }
    }

}
