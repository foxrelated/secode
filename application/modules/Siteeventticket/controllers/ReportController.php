<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ReportController.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_ReportController extends Seaocore_Controller_Action_Standard {

    public function indexAction() {

        //VALIDATIONS
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->event_id = $event_id = $this->_getParam('event_id', null);

        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$this->_helper->requireAuth()->setAuthParams($siteevent, $viewer, "edit")->isValid()) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        Zend_Registry::set('siteeventDashboardMenuActive', 'siteevent_dashboard_salesreports');
        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation("siteeventticket_main");

        $this->view->call_same_action = $this->_getParam('call_same_action', 0);
        $this->view->tab = $tab = $this->_getParam('tab', 0);
        $orderTable = Engine_Api::_()->getDbtable('orders', 'siteeventticket');
        $orderTableName = $orderTable->info('name');

        // to calculate the oldest order's creation year
        $select = $orderTable->select();
        $select->from($orderTableName, array('order_id', 'MIN(creation_date) as min_year'))
                ->group('order_id')
                ->limit(1);
        $min_year = $orderTable->fetchRow($select);
        $date = explode(' ', $min_year['min_year']);
        $yr = explode('-', $date[0]);
        $current_yr = date('Y', time());
        $year_array = array();
        $this->view->no_ads = 0;
        if (empty($min_year)) {
            $this->view->no_ads = 1;
        }
        $year_array[$current_yr] = $current_yr;
        while ($current_yr != $yr[0]) {
            $current_yr--;
            $year_array[$current_yr] = $current_yr;
        }

        $this->view->reportform = $reportform = new Siteeventticket_Form_Report(array('eventId' => $event_id, 'eventName' => $siteevent->getTitle()));
        $reportform->year_start->setMultiOptions($year_array);
        $reportform->year_end->setMultiOptions($year_array);

        // POPULATE FORM
        if (isset($_GET['generate_report'])) {
            $reportform->populate($_GET);

            // Get Form Values
            $values = $reportform->getValues();

            if (($values['select_event'] == 'specific_event') && empty($values['event_ids'])) {
                $reportform->addError('Must fill event name');
                return;
            }

            if (($values['select_ticket'] == 'specific_ticket') && empty($values['ticket_ids'])) {
                $reportform->addError('Must fill ticket name');
                return;
            }

            $start_cal_date = $values['start_cal'];
            $end_cal_date = $values['end_cal'];
            $start_tm = strtotime($start_cal_date);
            $end_tm = strtotime($end_cal_date);
            $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
            $url_values = explode('?', $url_string);

            if (empty($values['format_report'])) {
                $url = 'http://' . $_SERVER['HTTP_HOST'] . $this->view->url(array('action' => 'export-webpage', 'event_id' => $event_id, 'start_daily_time' => $start_tm, 'end_daily_time' => $end_tm), 'siteeventticket_report_general', true) . '?' . $url_values[1];
            } else {
                $url = 'http://' . $_SERVER['HTTP_HOST'] . $this->view->url(array('action' => 'export-excel', 'event_id' => $event_id, 'start_daily_time' => $start_tm, 'end_daily_time' => $end_tm), 'siteeventticket_report_general', true) . '?' . $url_values[1];
            }
            // Session Object
            $session = new Zend_Session_Namespace('emptySellerReport');
            if (isset($session->empty_session) && !empty($session->empty_session)) {
                unset($session->empty_session);
            } else {
                header("Location: $url");
            }
        }
        $this->view->empty = $this->_getParam('empty', 0);
    }

    // IF REPORT FORMAT IS EXCEL
    public function exportExcelAction() {

        if (!$this->_helper->requireUser()->isValid())
            return;

        Zend_Registry::set('siteeventDashboardMenuActive', 'siteevent_dashboard_salesreports');
        $this->view->event_id = $event_id = $_GET['event_id'];
        $owner_id = Engine_Api::_()->getItem('siteevent_event', $event_id)->owner_id;

        if (!empty($_GET)) {
            $this->_helper->layout->setLayout('default-simple');
            $values = $_GET;

            if (empty($values['event_ids']) && !empty($values['event_id'])) {
                $values['select_event'] = 'current_event';
            } else if (!empty($values['event_ids'])) {
                $values['select_event'] = 'specific_event';
            }

            $values = array_merge(array(
                'start_daily_time' => $this->_getParam('start_daily_time', time()),
                'end_daily_time' => $this->_getParam('end_daily_time', time()),
                'owner_id' => $owner_id,
                    ), $values);

            $this->view->rawdata = $rawdata = Engine_Api::_()->getDbTable('orders', 'siteeventticket')->getReports($values);
            $this->view->values = $values;
            $rawdata_array = $rawdata->toarray();
            $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
            $url_values = explode('?', $url_string);
            $url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->view->url(array('action' => 'index', 'event_id' => $event_id, 'empty' => '1', 'tab' => '1'), 'siteeventticket_report_general', true) . '?' . $url_values[1];
            if (empty($rawdata_array)) {
                // Session Object
                $session = new Zend_Session_Namespace('emptySellerReport');
                $session->empty_session = 1;
                header("Location: $url");
            }
        }

        $this->view->currencySymbol = $currencySymbol = Zend_Registry::isRegistered('siteeventticket.currency.symbol') ? Zend_Registry::get('siteeventticket.currency.symbol') : null;
        if (empty($currencySymbol)) {
            $this->view->currencySymbol = Engine_Api::_()->siteeventticket()->getCurrencySymbol();
        }
    }

    // IF REPORT FORMAT IS WEBPAGE
    public function exportWebpageAction() {

        if (!$this->_helper->requireUser()->isValid())
            return;

        Zend_Registry::set('siteeventDashboardMenuActive', 'siteevent_dashboard_salesreports');
        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation("siteeventticket_main");

        $this->view->event_id = $event_id = $_GET['event_id'];
        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        $this->view->reportform = $reportform = new Siteeventticket_Form_Report(array('event_id' => $event_id));
        $reportform->populate($_GET);

        $owner_id = Engine_Api::_()->getItem('siteevent_event', $event_id)->owner_id;

        // Get Form Values
        $values = $reportform->getValues();

        $start_daily_time = $this->_getParam('start_daily_time', time());
        $end_daily_time = $this->_getParam('end_daily_time', time());

        if (!empty($_GET)) {
            $values = $_GET;

            if (empty($values['event_ids']) && !empty($values['event_id'])) {
                $values['select_event'] = 'current_event';
            } else if (!empty($values['event_ids'])) {
                $values['select_event'] = 'specific_event';
            }

            $values = array_merge(array(
                'start_daily_time' => $start_daily_time,
                'end_daily_time' => $end_daily_time,
                'owner_id' => $owner_id,
                    ), $values);
            $this->view->values = $values;
            $this->view->report_type = $values['report_depend'];
            $this->view->rawdata = $rawdata = Engine_Api::_()->getDbTable('orders', 'siteeventticket')->getReports($values);

            $rawdata_array = $rawdata->toarray();
            $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
            $url_values = explode('?', $url_string);
            $url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->view->url(array('action' => 'index', 'event_id' => $event_id, 'empty' => '1', 'tab' => '1'), 'siteeventticket_report_general', true) . '?' . $url_values[1];
            if (empty($rawdata_array)) {
                // Session Object
                $session = new Zend_Session_Namespace('emptySellerReport');
                $session->empty_session = 1;
                header("Location: $url");
            }
        }

        $this->view->currencySymbol = $currencySymbol = Zend_Registry::isRegistered('siteeventticket.currency.symbol') ? Zend_Registry::get('siteeventticket.currency.symbol') : null;
        if (empty($currencySymbol)) {
            $this->view->currencySymbol = Engine_Api::_()->siteeventticket()->getCurrencySymbol();
        }
    }

    // To display events in the auto suggest at report form
    public function suggesteventsAction() {
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $text = $this->_getParam('search');
        $event_ids = $this->_getParam('event_ids', null);
        $limit = $this->_getParam('limit', 40);
        $pageTable = Engine_Api::_()->getDbtable('events', 'siteevent');
        $select = $pageTable->select()->where('title LIKE ?', '%' . $text . '%');

        if (!empty($event_ids)) {
            $select->where("event_id NOT IN ($event_ids)");
        }
        if (!empty($viewer_id)) {
            $select->where("owner_id =?", $viewer_id);
        }

        $select->order('title ASC')->limit($limit);
        $pageObjects = $pageTable->fetchAll($select);

        $data = array();
        $mode = $this->_getParam('struct');
        if ($mode == 'text') {
            foreach ($pageObjects as $pages) {
                $data[] = $pages->title;
            }
        } else {
            foreach ($pageObjects as $pages) {
                $data[] = array(
                    'id' => $pages->event_id,
                    'label' => $pages->title,
                    'photo' => $this->view->itemPhoto($pages, 'thumb.icon'),
                );
            }
        }

        if ($this->_getParam('sendNow', true)) {
            return $this->_helper->json($data);
        } else {
            $this->_helper->viewRenderer->setNoRender(true);
            $data = Zend_Json::encode($data);
            $this->getResponse()->setBody($data);
        }
    }

    // To display tickets in the auto suggest at report form
    public function suggestticketsAction() {
        $owner_id = $this->_getParam('owner_id', '');
        $text = $this->_getParam('search', $this->_getParam('value'));
        $event_ids = $this->_getParam('event_ids', null);
        $event_id = $this->_getParam('event_id', null);
        $select_event = $this->_getParam('select_event', null);
        $ticket_ids = $this->_getParam('ticket_ids', null);
        $limit = $this->_getParam('limit', 40);
        $ticketCreateFlag = $this->_getParam('create', null);

        if (!empty($ticketCreateFlag))
            $event_ids = $this->_getParam('event_id');

        $ticketTypes = array();
        $selectedTicketTypes = @implode(',', $ticketTypes);

        if (($select_event == 'specific_event') && empty($event_ids))
            $event_ids = $this->_getParam();

        if ($select_event == 'current_event')
            $event_ids = $event_id;

        $ticketObjects = Engine_Api::_()->getDbtable('tickets', 'siteeventticket')->getTicketsByText($event_ids, $text, $limit, $ticket_ids, $owner_id);
        $data = array();

        $mode = $this->_getParam('struct');
        if ($mode == 'text') {
            foreach ($ticketObjects as $tickets) {
                $data[] = $tickets->title;
            }
        } else {
            foreach ($ticketObjects as $tickets) {
                $data[] = array(
                    'id' => $tickets->ticket_id,
                    'label' => $tickets->title,
                    'price' => $tickets->price,
                );
            }
        }

        if ($this->_getParam('sendNow', true)) {
            return $this->_helper->json($data);
        } else {
            $this->_helper->viewRenderer->setNoRender(true);
            $data = Zend_Json::encode($data);
            $this->getResponse()->setBody($data);
        }
    }

    public function salesStatisticsAction() {
        //ONLY LOGGED IN USER 
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->event_id = $event_id = $this->_getParam('event_id', null);
        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        Zend_Registry::set('siteeventDashboardMenuActive', 'siteevent_dashboard_salesreports');

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$this->_helper->requireAuth()->setAuthParams($siteevent, $viewer, "edit")->isValid()) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //EVENT ID 

        $this->view->call_same_action = $this->_getParam('call_same_action', 0);
        $this->view->tab = $tab = $this->_getParam('tab', 0);
        if (isset($_POST['is_ajax']) && $_POST['is_ajax']) {
            $this->view->only_list_content = true;
        }
    }

}
