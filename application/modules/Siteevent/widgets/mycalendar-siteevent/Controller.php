<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Widget_MycalendarSiteeventController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        $this->view->quick = $this->_getParam('quick', 1);
        // get viewer
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $param['viewer_id'] = $viewer_id;
        $this->view->isajax = $isajax = $this->_getParam('is_ajax', false);
        $this->view->prev_next = $this->_getParam('prev_next', 'prev');
        $this->view->form = $form = new Siteevent_Form_Member_Join();
        if ($isajax) {
            $this->getElement()->removeDecorator('Title');
            $this->getElement()->removeDecorator('Container');
        }
        $this->view->actionLinks = $actionLinks = $this->_getParam('actionLinks', array('events', 'diaries', 'createNewEvent', 'invites'));
        $this->view->managePage = 0;
        $params1 = $this->_getParam('calendar_params', array());
        $params2 = $this->_getAllParams();
        $params = array_merge($params1, $params2);
        if (!isset($params['viewtype']))
            $params['viewtype'] = $this->_getParam('viewtype', 'calendar');

        $params['page'] = $this->_getParam('page', 1);
        $this->view->viewtype = $params['viewtype'];

        if (!isset($params['limit']))
            $params['limit'] = $this->_getParam('itemCount', 10);

        $this->view->postedby = $params['postedby'] = $this->_getParam('postedby', 1);
        $this->view->statistics = $params['statistics'] = $this->_getParam('statistics', array("viewCount", "likeCount", "commentCount", "memberCount", "reviewCount"));
        $this->view->showContent = $params['showContent'] = $this->_getParam('showContent', array("price", "location"));
        $front = Zend_Controller_Front::getInstance();
        $module = $front->getRequest()->getModuleName();
        $action = $front->getRequest()->getActionName();
        if (!isset($params['ismanage']) && $module == 'siteevent' && $action == 'manage') {
            $params['user_id'] = $viewer_id;
            $params['ismanage'] = 1;
        }
        
        $this->view->params = $params;
        //GET THE NO. OF INVITATION COUNT
        $this->view->invite_count = Engine_Api::_()->getDbTable('membership', 'siteevent')->getInviteCount($viewer_id);
        //CASE:1 SHOW THE CALENDAR VIEW

        if ($params['viewtype'] == 'calendar') {
            $param['display_today_birthday'] = "M";
            $param['limit'] = 0;
            $param['active_month'] = time();
            $params['event_occurrences'] = 1;

            // CALENDER WIDGET VIEW FUNCTIONALITY
            // GET THE MONTH FROM URL IF PRESENT OTHERWISE SET IT TO THE CURRENT MONTH
            $date = $this->_getParam('date_current', null);
            if (empty($date)) {
                $date = time();
            }

            // GET THIS, LAST AND NEXT MONTHS
            $this->view->date_current = $date = mktime(23, 59, 59, date("m", $date), 1, date("Y", $date));
            $this->view->date_next = $date_next = mktime(23,59, 59, date("m", $date) + 1, 1, date("Y", $date));
            $this->view->date_last = $date_last = mktime(23, 59, 59, date("m", $date) - 1, 1, date("Y", $date));

            $this->view->noOfDays = $days_in_month = date('t', $date);
            //GET THE LAST DATE OF MONTH
            $startdate = date("Y", $date) . '-' . date("m", $date) . '-' . 01;
            $lastDateofMonth = date("Y", $date) . '-' . date("m", $date) . '-' . $days_in_month . ' 23:59:59';
            $dateInfo = Engine_Api::_()->siteevent()->userToDbDateTime(array('starttime' => $startdate, 'endtime' => $lastDateofMonth));

            $date = $dateInfo['starttime'];
            $lastDateofMonth = $dateInfo['endtime'];
            $params['calendarlist'] = 1;
            $params['starttime'] = $date;
            $params['endtime'] = $lastDateofMonth;
            $params['sql'] = 'mycalendarlist';
            $paramsContentType = '';
            $this->view->monthEventResults = $monthEventResults = Engine_Api::_()->getDbTable('events', 'siteevent')->getEvent($paramsContentType, $params);

            //RE-ARRANGE THE RESULTS ACCORDING TO MONTH DAYS.
            $monthEventResults_Temp = array();
            $oldTz = date_default_timezone_get();
            foreach ($monthEventResults as $event) {
                $start = strtotime($event['starttime']);
                date_default_timezone_set($viewer->timezone);
                $day = date("j", $start);
                $startdate = date('Y-m-d G:i:s', $start);
                $event['starttime_database'] = $event['starttime'];
                $event['starttime'] = $startdate;
                date_default_timezone_set($oldTz);
                $monthEventResults_Temp [$day][] = $event;
            }
           
            $this->view->monthEventResults = $monthEventResults_Temp;

            //GET THE NUMBER OF DAYS IN THE MONTH
            //GET THE FIRST DAY OF THE MONTH
            $date = $this->view->date_current;
            $first_day_of_month = date("w", $date);
            if ($first_day_of_month == 0) {
                $first_day_of_month = 7;
            }
            $this->view->first_day_of_month = $first_day_of_month;

            //GET THE LAST DAY OF THE MONTH
            $this->view->last_day_of_month = $last_day_of_month = ($first_day_of_month - 1) + $days_in_month;

            //GET THE TOTAL NUMBER OF CELLS TO BE DISPLAYED IN THE CALENDER TABLE
            $this->view->total_cells = $total_cells = 35;

            //GET CURRENT MONTH THAT HAS TO BE DISPLAYED
            $this->view->current_month = $current_month = date("m", $date);

            //GET THE TEXT OF THE CURRENT MONTH
            $this->view->current_month_text = $this->view->locale()->toDate($date, array('format' => 'MMMM'));


            //GET THE YEAR OF THE CURRENT MONTHS		
            $this->view->current_year =  date("Y", $date);

            // get the base url
            $this->view->sugg_baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        }
        //END OF CALENDAR VIEW


        if ($params['viewtype'] == 'list') {
            //CASE:2 SHOW THE CALENDAR LISTING


            $datetime = $this->_getParam('date_current', null);

            $this->view->current_date = $datetime;
            //NOW FIND THE START TIME AND END TIME OF THE DATE
            $starttime = $datetime;
            $endtime = $datetime + (24 * 3600 - 1);
            $paramsContentType = '';
            $params['date_current'] = $datetime;
            $params['calendarlist'] = 1;
            $params['starttime'] = $starttime;
            $params['endtime'] = $endtime;
            $params['sql'] = 'list';
            $this->view->params = $params;

            $this->view->paginator = $paginator = $siteeventCalendar = Engine_Api::_()->getDbTable('events', 'siteevent')->getEvent($paramsContentType, $params);
            $this->view->totalCount = $paginator->getTotalItemCount();
            $this->view->postedby = $this->_getParam('postedby', 1);
            $this->view->showContent = $this->_getParam('showContent', array("price", "location"));
        }
        //END OF CALENDAR LISTING VIEW
        
        $this->view->showWaitlistLink = false;
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.waitlist', 1)) {
            $totalEventsInWaiting = Engine_Api::_()->getDbTable('waitlists', 'siteevent')->getColumnValue(array('user_id' => $viewer_id, 'columnName' => 'COUNT(*) AS totalEventsInWaiting'));

            $this->view->showWaitlistLink = $totalEventsInWaiting ? true : false;
        }        
    }

}