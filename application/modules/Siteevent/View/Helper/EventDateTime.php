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
class Siteevent_View_Helper_EventDateTime extends Zend_View_Helper_Abstract {

    public function eventDateTime($siteevent, $dateTimeInfo = array()) {

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

								if (isset($dateTimeInfo['contentFullWidth']) && !empty($dateTimeInfo['contentFullWidth'])) {
                echo '<div class="siteevent_listings_stats">
                      <div class="o_hidden" ' . $dateTimeLabel . '><i class="siteevent_icon_strip siteevent_icon siteevent_icon_time" title="' . $iconHtmlTitle . '"></i>';
								} else {
									 echo '<div class="siteevent_listings_stats">
                      <i class="siteevent_icon_strip siteevent_icon siteevent_icon_time" title="' . $iconHtmlTitle . '"></i>
                      <div class="o_hidden" ' . $dateTimeLabel . '>';
								}

                $starttimeFull = $startTitle . $view->locale()->toDateTime($eventStartDateTime, array('size' => 'full'));
                if ($dateTimeInfo['showStartDateTime'] && !$dateTimeInfo['showEndDateTime']) {
                    $starttime = $view->locale()->toDate($eventStartDateTime, array('format' => 'M/d/yyyy'));
                    if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
                        echo '<a title="' . $starttimeFull . '" href="javascript:void(0);" onclick="getDayEvents(' . strtotime($starttime) . ');">' . $startDateTime . '</a>';
                    } else {
                        echo $startDateTime;
                    }
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

                            if (isset($dateTimeInfo['contentFullWidth']) && !empty($dateTimeInfo['contentFullWidth'])) {
                                echo ' - <img src="' . $view->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/images/arrow-right.png" alt="" title="' . $view->translate("End Date") . '"/>';
                            } else {
                                echo '<br/><img src="' . $view->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/images/arrow-right.png" alt="" title="' . $view->translate("End Date") . '"/><i></i>';
                            }

                            echo "<span title='$endDateFull'>$endDateTime</span>";
                        }
                    } else {
                        echo "<span title='$endDateFull'>$endDateTime</span>";
                    }
                }
                $totalEventOccurrences = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getOccurrenceCount($siteevent->event_id);
                if (!empty($siteevent->repeat_params) && $totalEventOccurrences > 1 && (!isset($dateTimeInfo['showMultipleText']) || $dateTimeInfo['showMultipleText'])) {
                    echo $view->translate(" (Multiple Dates Available)");
                }
                echo '</div>
                    </div>';
            }
        }
    }

}
