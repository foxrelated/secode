<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Widget_CalendarviewSiteeventController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    $params = $this->_getAllParams();
    $this->view->params = $params;
    $this->view->loaded_by_ajax = $loaded_by_ajax = $this->_getParam('loaded_by_ajax', true);
    
    // get viewer
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $param['viewer_id'] = $viewer_id;
    $this->view->isajax = $isajax = $this->_getParam('is_ajax', false);

    if ($isajax) {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }

    $this->view->categoryIds = Engine_Api::_()->getDbTable('categories', 'siteevent')->getParentCategories();
    $this->view->siteeventCalenderViewType = $siteeventCalenderViewType = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventcalender.viewtype', 1);
    $siteeventCalenderView = Zend_Registry::isRegistered('siteeventCalenderView') ? Zend_Registry::get('siteeventCalenderView') : null;
    // GET CATEGORY
    $this->view->category_id = $category_id = $this->_getParam('category_id', null);
    $params = $this->_getAllParams();

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
    if ($module == 'siteevent' && $action == 'manage')
      $eventsCountType = $this->_getParam('siteevent_calendar_event_count_type', 'all');
    else
      $eventsCountType = $this->_getParam('siteevent_calendar_event_count_type', 'all');

    if ((!isset($params['ismanage']) && $module == 'siteevent' && $action == 'manage') || ($eventsCountType == 'onlyjoined' || $eventsCountType == 1)) {
      $params['user_id'] = $viewer_id;
      $params['ismanage'] = 1;
    }
    if ($eventsCountType == 1 || $eventsCountType == 'onlyjoined')
      $params['siteevent_calendar_event_count_type'] = 'onlyjoined';
    else
      $params['siteevent_calendar_event_count_type'] = 'all';

    $this->view->params = $params;

    if (empty($siteeventCalenderView))
      return $this->setNoRender();

    //CASE:1 SHOW THE CALENDAR VIEW

    if ($params['viewtype'] == 'calendar') {
      $params['sql'] = 'sidecalendar';
      $this->view->params = $params;
      //if($display_action == 3) {
      $param['display_today_birthday'] = "M";
      $param['limit'] = 0;
      $param['active_month'] = time();


      // CALENDER WIDGET VIEW FUNCTIONALITY
      // GET THE MONTH FROM URL IF PRESENT OTHERWISE SET IT TO THE CURRENT MONTH
      $date = $this->_getParam('date_current', null);
      if (empty($date)) {
        $date = time();
      }

      // GET THIS, LAST AND NEXT MONTHS
      $this->view->date_current = $date = mktime(23, 59, 59, date("m", $date), 1, date("Y", $date));
      $this->view->date_next = $date_next = mktime(0, 0, 0, date("m", $date) + 1, 1, date("Y", $date));
      $this->view->date_last = $date_last = mktime(0, 0, 0, date("m", $date) - 1, 1, date("Y", $date));

      $days_in_month = date('t', $date);
      //GET THE LAST DATE OF MONTH      
      $startdate = date("Y", $date) . '-' . date("m", $date) . '-' . 01;
      $lastDateofMonth = date("Y", $date) . '-' . date("m", $date) . '-' . $days_in_month . ' 23:59:59';

      $params['calendarlist'] = 1;
      
       $dateInfo = Engine_Api::_()->siteevent()->userToDbDateTime(array('starttime' => $startdate, 'endtime' => $lastDateofMonth));
      $date = $dateInfo['starttime'];
      $lastDateofMonth = $dateInfo['endtime'];
      $params['starttime'] = $date;
      $params['endtime'] = $lastDateofMonth;
      $params['sql'] = 'count';
      $paramsContentType = '';
      $this->view->detactLocation = $params['detactLocation'] = $this->_getParam('detactLocation', 0);
      if ($this->view->detactLocation) {
          $this->view->detactLocation = Engine_Api::_()->siteevent()->enableLocation();
      }
      if($isajax || !$loaded_by_ajax) {
        
        if ($this->view->detactLocation) { 
            $params['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
            $params['latitude'] = $this->_getParam('latitude', 0);
            $params['longitude'] = $this->_getParam('longitude', 0);
            $params['detactLocation'] = 1;
        }
        
        $params['eventType'] = $this->_getParam('eventType', 'All');
       
        $monthEventResults = Engine_Api::_()->getDbTable('events', 'siteevent')->getEvent($paramsContentType, $params);
      }

      $this->view->monthEventResults = '';
      if (($isajax && $monthEventResults) || !$loaded_by_ajax) {

        $this->view->monthEventResults = Engine_Api::_()->siteevent()->getEventDayCount($monthEventResults, $date, $lastDateofMonth);
      }
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
      $this->view->total_cells = $total_cells = (floor($last_day_of_month / 7) + 1) * 7;

      //GET CURRENT MONTH THAT HAS TO BE DISPLAYED
      $this->view->current_month = $current_month = date("m", $date);

      //GET THE TEXT OF THE CURRENT MONTH
      $this->view->current_month_text = $this->view->locale()->toDate($date, array('format' => 'MMMM'));

      //GET THE YEAR OF THE CURRENT MONTHS		
      $this->view->current_year = date("Y", $date);

      // get the base url
      $this->view->sugg_baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
    }
    //END OF CALENDAR VIEW


    if ($params['viewtype'] == 'list') {
      //CASE:2 SHOW THE CALENDAR LISTING


      $date = $this->_getParam('date_current', null);
      $starttime = date("Y-m-d H:i:s", $date);
      $endtime = date("Y-m-d H:i:s", $date + (24 * 3600 - 1));
      $dateInfo = Engine_Api::_()->siteevent()->userToDbDateTime(array('starttime' => $starttime, 'endtime' => $endtime));
$timezone = Engine_Api::_()->getApi('settings', 'core')->core_locale_timezone;
      if ($viewer->getIdentity()) {
        $timezone = $viewer->timezone;
      }
      //$starttime = date("Y-m-d", $date);
      $todaysDate = date("Y-m-d", time());
      $oldTz = date_default_timezone_get();
      date_default_timezone_set($timezone);
      //$starttime = strtotime($starttime);
     // $starttime = date("Y-m-d H:i:s", $starttime);echo $timezone;die;
      $todaysDate = strtotime($todaysDate);
      //$endtime = $starttime + (24 * 3600 - 1);
      date_default_timezone_set($oldTz);
//      $starttime = date("Y-m-d H:i:s", $starttime);
//      $endtime = date("Y-m-d H:i:s", $endtime);
      $this->view->todaysDate = $todaysDate;     
      $currentDate = Engine_Api::_()->siteevent()->userToDbDateTime(array('starttime' => $starttime));
      $this->view->current_date = $currentDate['starttime'];


      //NOW FIND THE START TIME AND END TIME OF THE DATE
      //$starttime = $datetime;
      $paramsContentType = '';
      //$params['date_current'] = $datetime;
      $params['calendarlist'] = 1;
      $params['starttime'] = $dateInfo['starttime'];
      $params['endtime'] = $dateInfo['endtime'];
      if ($this->_getParam('sql', null) == 'sidecalendar')
        $params['siteevent_calendar_event_count_type'] = $this->_getParam('siteevent_calendar_event_count_type', null);
      $params['sql'] = false;

      if (!empty($category_id))
        $params['category_id'] = $category_id;

      $params['eventType'] = $this->_getParam('eventType', 'All');
      
      $this->view->params = $params;
      
      $this->view->paginator = $paginator = $siteeventCalendar = Engine_Api::_()->getDbTable('events', 'siteevent')->getEvent($paramsContentType, $params);
      $this->view->totalCount = $paginator->getTotalItemCount();

      $this->view->postedby = $this->_getParam('postedby', 1);
      $this->view->showContent = $this->_getParam('showContent', array("price", "location"));
    }
    //END OF CALENDAR LISTING VIEW

    if (empty($siteeventCalenderViewType))
      return $this->setNoRender();
  }

}