<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: EventDateTime.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_View_Helper_EventDateTimeSMApp extends Zend_View_Helper_Abstract {

  public function eventDateTimeSMApp($siteevent, $dateTimeInfo = array()) {

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    $view->headScript()->appendFile($view->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/scripts/core.js');

    $datetimeFormat = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.datetime.format', 'medium');

    if ($dateTimeInfo['showStartDateTime'] || $dateTimeInfo['showEndDateTime']) {
      // FETCH START AND END DATETIME VALUE SAVED IN DATABASE
      if (isset($dateTimeInfo['showEventType']) && $dateTimeInfo['showEventType'] == 'all' && !empty($siteevent->repeat_params)) {
        $startEndDate = $siteevent->getNextOccurrenceDateTime();
      } else {
        if (isset($dateTimeInfo['occurrence_id']) && !empty($dateTimeInfo['occurrence_id'])) {
          $startEndDate = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getEventDate($siteevent->event_id, $dateTimeInfo['occurrence_id']);
        } else {
          $startEndDate = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getEventDate($siteevent->event_id);
        }
      }

      if (!empty($startEndDate)) {
        $eventStartDateTime = $startEndDate['starttime'];
        $eventEndDateTime = $startEndDate['endtime'];

        // SET ICON HTML TITLE
        if ($dateTimeInfo['showStartDateTime'] && $dateTimeInfo['showEndDateTime']) {
          $iconHtmlTitle = $view->translate("Start & End Date");
        } else if ($dateTimeInfo['showStartDateTime'] && !$dateTimeInfo['showEndDateTime']) {
          $iconHtmlTitle = $view->translate("Start Date");
        } else if (!$dateTimeInfo['showStartDateTime'] && $dateTimeInfo['showEndDateTime']) {
          $iconHtmlTitle = $view->translate("End Date");
        }

        //if (isset($dateTimeInfo['showDateTimeLabel']) && !empty($dateTimeInfo['showDateTimeLabel'])) {
        //    $dateTimeLabel = "title = '$iconHtmlTitle'";
        //} else {
        $dateTimeLabel = '';
        //}

        $startTitle = $view->translate("Start Date: ");
        $endTitle = $view->translate("End Date: ");

        $startDateTime = $view->locale()->toEventDateTime($eventStartDateTime, array('size' => $datetimeFormat));
        $endDateTime = $view->locale()->toEventDateTime($eventEndDateTime, array('size' => $datetimeFormat));


        $starttimeFull = $startTitle . $view->locale()->toDateTime($eventStartDateTime, array('size' => 'full'));
        if ($dateTimeInfo['showStartDateTime'] && !$dateTimeInfo['showEndDateTime']) {
          //$starttime = $view->locale()->toDate($eventStartDateTime, array('format' => 'M/d/yyyy'));
          echo '<span class="datemonth">
                <span class="month">' . $view->locale()->toDateTime($eventStartDateTime, array('format' => 'MMM')) . '</span>
                <span class="date">' . $view->locale()->toDateTime($eventStartDateTime, array('format' => 'dd')) . '</span>
              </span>
              <span class="list-stats f_small">' . $view->locale()->toTime($eventStartDateTime) . '</span>';
        } elseif ($dateTimeInfo['showStartDateTime']) {
          echo "<span title='$starttimeFull'>$startDateTime</span>";
        }

        if ($dateTimeInfo['showEndDateTime']) {
          $endDateFull = $endTitle . $view->locale()->toEventDateTime($eventEndDateTime, array('size' => 'full'));
          if ($dateTimeInfo['showStartDateTime'] && $dateTimeInfo['showEndDateTime']) {
            $startDate = $view->locale()->toEventDateTime($eventStartDateTime, array('format' => 'ddMMyy'));
            $endDate = $view->locale()->toEventDateTime($eventEndDateTime, array('format' => 'ddMMyy'));
            if ($startDate == $endDate) {
              echo ' - ' . $view->locale()->toEventTime($eventEndDateTime, array('size' => $datetimeFormat));
            } else {
              echo ' - ';
              echo "<span title='$endDateFull'>$endDateTime</span>";
            }
          } else {
            echo "<span title='$endDateFull'>$endDateTime</span>";
          }
        }
//        $totalEventOccurrences = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getOccurrenceCount($siteevent->event_id);
//        if (!empty($siteevent->repeat_params) && $totalEventOccurrences > 1 && (!isset($dateTimeInfo['showMultipleText']) || $dateTimeInfo['showMultipleText'])) {
//          echo $view->translate(" (Multiple Dates Available)");
//        }
      }
    }
  }

}
