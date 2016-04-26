<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AddToDiary.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_View_Helper_EventInfoSMApp extends Zend_View_Helper_Abstract {

    public function eventInfoSMApp($siteevent, $eventInfo, $params = array()) { 

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $locationEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1);
        $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
        $categoryRouteName = Engine_Api::_()->siteevent()->getCategoryHomeRoute();

         if (in_array('startDate', $eventInfo) || in_array('endDate', $eventInfo)) {
            $dateTimeInfo = array();
            $dateTimeInfo['occurrence_id'] = $siteevent->occurrence_id;
            $dateTimeInfo['showStartDateTime'] = in_array('startDate', $eventInfo);
            $dateTimeInfo['showEndDateTime'] = in_array('endDate', $eventInfo);

            if (isset($params['showEventType']))
                $dateTimeInfo['showEventType'] = $params['showEventType'];
            else
                $dateTimeInfo['showEventType'] = '';

            $view->eventDateTimeSMApp($siteevent, $dateTimeInfo);
         }
            
          if ($locationEnabled && !empty($siteevent->location) && in_array('location', $eventInfo)) {
            $truncationLocation = 40;
            if (isset($params['truncationLocation']) && !empty($params['truncationLocation'])) {
                $truncationLocation = $params['truncationLocation'];
            }
            $location = Engine_Api::_()->seaocore()->seaocoreTruncateText($siteevent->location, $truncationLocation);
              echo "<span title='$siteevent->location' class='list-stats f_small'>". $view->translate(" at ").$location."</span>";
        }
        echo '<span class="list-stats f_small">';
        
         $statistics = '';

        if (!Engine_Api::_()->siteevent()->isTicketBasedEvent() && in_array('memberCount', $eventInfo)) { 
            $statistics .= $view->translate(array('%s guest', '%s guests', $siteevent->member_count), $view->locale()->toNumber($siteevent->member_count)) . ', ';
        }

        if (!empty($eventInfo) && in_array('commentCount', $eventInfo)) {
            $statistics .= $view->translate(array('%s comment', '%s comments', $siteevent->comment_count), $view->locale()->toNumber($siteevent->comment_count)) . ', ';
        }

        if (!empty($eventInfo) && in_array('viewCount', $eventInfo)) {
            $statistics .= $view->translate(array('%s view', '%s views', $siteevent->view_count), $view->locale()->toNumber($siteevent->view_count)) . ', ';
        }

        if (!empty($eventInfo) && in_array('likeCount', $eventInfo)) {
            $statistics .= $view->translate(array('%s like', '%s likes', $siteevent->like_count), $view->locale()->toNumber($siteevent->like_count)) . ', ';
        }

        if (isset($params['most_discuss_widget']) && !empty($params['most_discuss_widget'])) {
            $statistics .= $view->translate(array('%s Discussion', '%s Discussions', $siteevent->counttopics), $view->locale()->toNumber($siteevent->counttopics)) . ', ';
            $statistics .= $view->translate(array('%s Reply', '%s Replies', $siteevent->total_count), $view->locale()->toNumber($siteevent->total_count));
        }

        $statistics = trim($statistics);
        $statistics = rtrim($statistics, ',');

        if (!empty($statistics)) {
            echo $statistics;
        }
         if (!empty($statistics) && in_array('hostName', $eventInfo)) { 
        echo " | "; 
         }
        if (in_array('hostName', $eventInfo)) {
//            if (isset($params['view_type']) && $params['view_type'] == 'grid_view') { //to be done
                $hostDisplayName = $siteevent->getHostName(true);
                if (!empty($hostDisplayName)) {
                    if (!empty($params['titlePosition']))
                        $className = 'siteevent_listings_host_h';
                    else
                        $className = '';
                    if (is_array($hostDisplayName)) {
                        echo $view->translate("Hosted by ") . '
                        <b>' . Engine_Api::_()->seaocore()->seaocoreTruncateText($hostDisplayName['displayName'], 12) . '</b>';
                    } else {
                        echo '<b>' . Engine_Api::_()->seaocore()->seaocoreTruncateText($hostDisplayName,12) . '</b>';
                    }
                }
//            } else {
//                $hostDisplayName = $siteevent->getHostName();
//                if (!empty($hostDisplayName)) {
//                    echo '<span class="list-stats f_small"><b>' . Engine_Api::_()->seaocore()->seaocoreTruncateText($hostDisplayName,25) . ' </b></span>';
//                }
//            }
        }
        echo '</span>';

        $totalEventOccurrences = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getOccurrenceCount($siteevent->event_id);
        if (!empty($siteevent->repeat_params) && $totalEventOccurrences > 1 && (!isset($dateTimeInfo['showMultipleText']) || $dateTimeInfo['showMultipleText'])) {
          echo "<span class='list-stats f_small'>" .$view->translate( "(Multiple Dates Available)" ). "</span>";
        }
        
         if (in_array('venueName', $eventInfo) && !$siteevent->is_online && !empty($siteevent->venue_name)) {
            echo '<span class="list-stats f_small">' .
            $siteevent->venue_name . '
          </span>';
        }    
                      
        if (in_array('price', $eventInfo) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.price', 0) ) {
          if(!empty($siteevent->price) && $siteevent->price > 0) {
							echo '<span class="list-stats f_small">' .
							$view->locale()->toCurrency($siteevent->price, $currency) .
							'</span>';
					} else {
						echo '<span class="list-stats f_small">' .
							$view->translate("FREE") .
							'</span>';
					}
        }

        if (in_array('categoryLink', $eventInfo) && $siteevent->category_id) {
            echo '<span class="list-stats f_small">'. 
            $siteevent->getCategory()->getTitle(true) .
            '</span>';
        }

        if (in_array('ledBy', $eventInfo)) {
            $ledBys = $siteevent->getEventLedBys(in_array('hostName', $eventInfo));
            if (!empty($ledBys)) {
                echo '<span class="list-stats f_small">' .
                strip_tags($ledBys) .
                '</span>';
            }
        }


        if ((in_array('ratingStar', $eventInfo) && !empty($siteevent->review_count)) || (in_array('reviewCount', $eventInfo) && (!empty($siteevent->rating_editor) || !empty($siteevent->rating_users) || !empty($siteevent->$params['ratingValue']) ))) {
            if (in_array('ratingStar', $eventInfo) && in_array('reviewCount', $eventInfo))
                $iconHtmlTitle = $view->translate("Reviews & Ratings");
            else if (in_array('ratingStar', $eventInfo) && !in_array('reviewCount', $eventInfo))
                $iconHtmlTitle = $view->translate("Ratings");
            else if (!in_array('ratingStar', $eventInfo) && in_array('reviewCount', $eventInfo))
                $iconHtmlTitle = $view->translate("Reviews");

            echo '<span class="list-stats f_small">
            <span class="fleft">';
            if (in_array('ratingStar', $eventInfo)) {
                if ($params['ratingValue'] == 'rating_both') {
                    echo $view->ShowRatingStarSiteeventSM($siteevent->rating_editor, 'editor', $params['ratingShow']);
                    echo $view->ShowRatingStarSiteeventSM($siteevent->rating_users, 'user', $params['ratingShow']);
                }
                else
                    echo $view->ShowRatingStarSiteeventSM($siteevent->$params['ratingValue'], $params['ratingType'], $params['ratingShow']);
            }
            echo '</span>';
            if (in_array('reviewCount', $eventInfo)) {
                echo '<span class="fleft f_small"> ';
                echo $view->translate(array('%s review', '%s reviews', $siteevent->review_count), $view->locale()->toNumber($siteevent->review_count));
                echo '</span>';
            }
            echo '</span>';
        }
    }
}