<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminReportController.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_AdminReportController extends Core_Controller_Action_Admin {

    public function indexAction() {


        //TAB CREATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteevent_admin_main', array(), 'siteeventticket_admin_main_ticket');

        $this->view->navigationGeneral = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteeventticket_admin_main_ticket', array(), 'siteevent_admin_main_report');

        $this->view->reportType = $reportType = $this->_getParam('type', 0);

        $orderTable = Engine_Api::_()->getDbtable('orders', 'siteeventticket');
        $orderTableName = $orderTable->info('name');

        // to calculate the oldest order's creation year
        $select = $orderTable->select()->from($orderTableName, array('order_id', 'MIN(creation_date) as min_year'))->group('order_id')->limit(1);
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

        include APPLICATION_PATH . '/application/modules/Siteeventticket/controllers/license/license2.php';
        $this->view->reportform = $reportform = new Siteeventticket_Form_Admin_Report(array('reportType' => $reportType));
        $reportform->year_start->setMultiOptions($year_array);
        $reportform->year_end->setMultiOptions($year_array);

        // POPULATE FORM
        if (isset($_GET['generate_report'])) {
            $reportform->populate($_GET);

            // Get Form Values
            $values = $reportform->getValues();
            $report_form_error = false;

            if (($values['select_event'] == 'specific_event') && empty($values['event_ids'])) {
                $reportform->addError('Must fill event name');
                $report_form_error = true;
            }

            if (!empty($reportType)) {
                if (($values['select_ticket'] == 'specific_ticket') && empty($values['ticket_ids'])) {
                    $reportform->addError('Must fill ticket name');
                    $report_form_error = true;
                }
            }

            if (!empty($report_form_error))
                return;

            $start_cal_date = $values['start_cal'];
            $end_cal_date = $values['end_cal'];
            $start_tm = strtotime($start_cal_date);
            $end_tm = strtotime($end_cal_date);
            $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
            $url_values = explode('?', $url_string);

            if (empty($values['format_report'])) {
                $url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->view->url(array('module' => 'siteeventticket', 'controller' => 'report', 'action' => 'export-webpage', 'start_daily_time' => $start_tm, 'end_daily_time' => $end_tm, 'type' => $reportType), 'admin_default', true) . '?' . $url_values[1];
            } else {
                $url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->view->url(array('module' => 'siteeventticket', 'controller' => 'report', 'action' => 'export-excel', 'start_daily_time' => $start_tm, 'end_daily_time' => $end_tm, 'type' => $reportType), 'admin_default', true) . '?' . $url_values[1];
            }
            // Session Object
            $session = new Zend_Session_Namespace('empty_adminredirect');
            if (isset($session->empty_session) && !empty($session->empty_session)) {
                unset($session->empty_session);
            } else {
                header("Location: $url");
            }
        }
        $this->view->empty = $this->_getParam('empty', 0);
    }

    // in case of admin's report format is excel file, the form action is redirected to this action
    public function exportExcelAction() {

        $this->view->post = $post = 0;
        $this->view->reportType = $reportType = $this->_getParam('type', 0);
        $start_daily_time = $this->_getParam('start_daily_time', time());
        $end_daily_time = $this->_getParam('end_daily_time', time());

        if (!empty($_GET)) {
            $this->_helper->layout->setLayout('default-simple');
            $this->view->post = $post = 1;
            $values = $_GET;
            $values = array_merge(array(
                'start_daily_time' => $start_daily_time,
                'end_daily_time' => $end_daily_time,
                'admin_report' => '1',
                'type' => $reportType,
                    ), $values);

            $this->view->values = $values;
            $this->view->rawdata = $rawdata = Engine_Api::_()->getDbTable('orders', 'siteeventticket')->getReports($values);

            $rawdata_array = $rawdata->toarray();
            $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
            $url_values = explode('?', $url_string);
            $url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->view->url(array('module' => 'siteeventticket', 'controller' => 'report', 'action' => 'index', 'type' => $reportType, 'empty' => '1'), 'admin_default', true) . '?' . $url_values[1];
            if (empty($rawdata_array)) {
                // Session Object
                $session = new Zend_Session_Namespace('empty_adminredirect');
                $session->empty_session = 1;
                header("Location: $url");
            }
        }

        $this->view->currencySymbol = $currencySymbol = Zend_Registry::isRegistered('siteeventticket.currency.symbol') ? Zend_Registry::get('siteeventticket.currency.symbol') : null;
        if (empty($currencySymbol)) {
            $this->view->currencySymbol = Engine_Api::_()->siteeventticket()->getCurrencySymbol();
        }
    }

    // in case of admin's report format is webpage, the form action is redirected to this action
    public function exportWebpageAction() {

        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteevent_admin_main', array(), 'siteevent_admin_main_report');

        $this->view->reportType = $reportType = $this->_getParam('type', 0);
        $this->view->post = $post = 0;
        $start_daily_time = $this->_getParam('start_daily_time', time());
        $end_daily_time = $this->_getParam('end_daily_time', time());

        if (!empty($_GET)) {
            $this->view->post = $post = 1;
            $values = $_GET;
            $values = array_merge(array(
                'start_daily_time' => $start_daily_time,
                'end_daily_time' => $end_daily_time,
                'admin_report' => '1',
                'type' => $reportType,
                    ), $values);
            $this->view->values = $values;

            $this->view->rawdata = $rawdata = Engine_Api::_()->getDbTable('orders', 'siteeventticket')->getReports($values);

            $rawdata_array = $rawdata->toarray();
            $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
            $url_values = explode('?', $url_string);
            $url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->view->url(array('module' => 'siteeventticket', 'controller' => 'report', 'action' => 'index', 'type' => $reportType, 'empty' => '1'), 'admin_default', true) . '?' . $url_values[1];
            if (empty($rawdata_array)) {
                // Session Object
                $session = new Zend_Session_Namespace('empty_adminredirect');
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
        $text = $this->_getParam('search');
        $event_ids = $this->_getParam('event_ids', null);
        $limit = $this->_getParam('limit', 40);
        $pageTable = Engine_Api::_()->getDbtable('events', 'siteevent');
        $select = $pageTable->select()
                ->where('title LIKE ?', '%' . $text . '%');
        if (!empty($event_ids)) {
            $select->where("event_id NOT IN ($event_ids)");
        }

        $select->order('title ASC')
                ->limit($limit);
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
        $text = $this->_getParam('search', $this->_getParam('value'));
        $event_ids = $this->_getParam('event_ids', null);
        $select_event = $this->_getParam('select_event', null);

        if (($select_event == 'specific_event') && empty($event_ids)) {
            return;
        }

        $ticket_ids = $this->_getParam('ticket_ids', null);
        $limit = $this->_getParam('limit', 40);

        $ticketObjects = Engine_Api::_()->getDbtable('tickets', 'siteeventticket')->getTicketsByText($event_ids, $text, $limit, $ticket_ids);
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
                    'photo' => $this->view->itemPhoto($tickets, 'thumb.icon'),
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

}
