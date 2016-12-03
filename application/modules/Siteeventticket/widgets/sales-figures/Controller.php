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
class Siteeventticket_Widget_SalesFiguresController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        //IF TICKET SETTING DISABLED FROM ADMIN SIDE
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.ticket.enabled', 1)) {
            return $this->setNoRender();
        }
        
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $event_id = $request->getParam('event_id', null);

        //VALIDATIONS
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$siteevent->authorization()->isAllowed($viewer, 'edit')) {
            return $this->setNoRender();
        }
        //END

        $orderTable = Engine_Api::_()->getDbTable('orders', 'siteeventticket');

        $this->view->todaySale = $orderTable->getEventEarning($event_id, 'today');
        $this->view->weekSale = $orderTable->getEventEarning($event_id, 'week');
        $this->view->monthSale = $orderTable->getEventEarning($event_id, 'month');
        $this->view->siteTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', '');
        $siteeventticketEventStatistics = Zend_Registry::isRegistered('siteeventticketEventStatistics') ? Zend_Registry::get('siteeventticketEventStatistics') : null;
        if(empty($siteeventticketEventStatistics)) {
          return $this->setNoRender();
        }

        //PAYMENT FLOW CHECK
        $this->view->paymentToSiteadmin = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.payment.to.siteadmin', 0);

        $this->view->currencySymbol = Zend_Registry::isRegistered('siteeventticket.currency.symbol') ? Zend_Registry::get('siteeventticket.currency.symbol') : null;
        if (empty($this->view->currencySymbol))
            $this->view->currencySymbol = Engine_Api::_()->siteeventticket()->getCurrencySymbol();
    }

}
