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
class Siteevent_View_Helper_EventInfoSM extends Zend_View_Helper_Abstract {

    public function eventInfoSM($siteevent, $eventInfo, $params = array()) { 

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $locationEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1);
        $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
        $categoryRouteName = Engine_Api::_()->siteevent()->getCategoryHomeRoute();

        if (in_array('hostName', $eventInfo)) {
            if (isset($params['view_type']) && $params['view_type'] == 'grid_view') {
                $hostDisplayName = $siteevent->getHostName(true);
                if (!empty($hostDisplayName)) {
                    if (!empty($params['titlePosition']))
                        $className = 'siteevent_listings_host_h';
                    else
                        $className = '';
                    if (is_array($hostDisplayName)) {
                        echo '<div class="siteevent_listings_stats siteevent_listings_host ' . $className . ' ">';
                        echo $hostDisplayName['displayImage'];
                        echo '<div class="o_hidden">
                <span class="f_small">' . $view->translate("Hosted by: ") . ' </span><br/> 
                <b>' . Engine_Api::_()->seaocore()->seaocoreTruncateText($hostDisplayName['displayName'], 12) . '</b><br />
              </div>
            </div>';
                    } else {
                        echo '<div class="siteevent_listings_stats siteevent_listings_host ' . $className . ' ">';
                        echo '<img class="thumb_icon item_photo_user item_nophoto " alt="" src="application/modules/User/externals/images/nophoto_user_thumb_icon.png">';
                        echo '<div class="o_hidden">
                <span class="f_small">' . $view->translate("Hosted by: ") . ' </span><br/> 
                <b>' . Engine_Api::_()->seaocore()->seaocoreTruncateText($hostDisplayName,12) . '</b><br />
              </div>
            </div>';
                    }
                }
            } else {

                $hostDisplayName = $siteevent->getHostName();
                if (!empty($hostDisplayName)) {
                    echo '<div class="siteevent_listings_stats">
                          <i class="siteevent_icon_strip siteevent_icon siteevent_icon_host" title="' . $view->translate("Host") . '"></i>
                          <div class="o_hidden">
                            <b> ' . Engine_Api::_()->seaocore()->seaocoreTruncateText($hostDisplayName,25) . ' </b>
                          </div>
                        </div>';
                }
            }
        }

        if (in_array('venueName', $eventInfo) && !$siteevent->is_online && !empty($siteevent->venue_name)) {
            echo '<div class="siteevent_listings_stats">
          <i class="siteevent_icon_strip siteevent_icon siteevent_icon_venue" title="' . $view->translate("Venue") . '"></i>
          <div class="o_hidden">' .
            $siteevent->venue_name . '
          </div>
        </div>';
        }

        if ($locationEnabled && !empty($siteevent->location) && in_array('location', $eventInfo)) {
            $truncationLocation = 40;
            if (isset($params['truncationLocation']) && !empty($params['truncationLocation'])) {
                $truncationLocation = $params['truncationLocation'];
            }
            $location = Engine_Api::_()->seaocore()->seaocoreTruncateText($siteevent->location, $truncationLocation);
            echo '<div class="siteevent_listings_stats">
          <i class="siteevent_icon_strip siteevent_icon siteevent_icon_location" title="' . $view->translate("Location") . '"></i>
          <div class="o_hidden">';
                echo "<span title='$siteevent->location'>$location</span>";
            echo '</div>
        </div>';
        }

        if (in_array('startDate', $eventInfo) || in_array('endDate', $eventInfo)) {
            $dateTimeInfo = array();
            $dateTimeInfo['occurrence_id'] = $siteevent->occurrence_id;
            $dateTimeInfo['showStartDateTime'] = in_array('startDate', $eventInfo);
            $dateTimeInfo['showEndDateTime'] = in_array('endDate', $eventInfo);

            if (isset($params['showEventType']))
                $dateTimeInfo['showEventType'] = $params['showEventType'];
            else
                $dateTimeInfo['showEventType'] = '';

            $view->eventDateTime($siteevent, $dateTimeInfo);
        }

        if (in_array('price', $eventInfo) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.price', 0) ) {
          if(!empty($siteevent->price) && $siteevent->price > 0) {
							echo '<div class="siteevent_listings_stats">
						<i class="siteevent_icon_strip siteevent_icon siteevent_icon_price" title="' . $view->translate("Price") . '"></i>
						<div class="o_hidden bold"> ' .
							$view->locale()->toCurrency($siteevent->price, $currency) .
							'</div>
					</div>';
					} else {
						echo '<div class="siteevent_listings_stats siteevent_listings_price_free">
						<i class="siteevent_icon_strip siteevent_icon siteevent_icon_price" title="' . $view->translate("Price") . '"></i>
						<div class="o_hidden bold"> ' .
							$view->translate("FREE") .
							'</div>
					</div>';
					}
        }

        if (in_array('categoryLink', $eventInfo) && $siteevent->category_id) {
            echo '<div class="siteevent_listings_stats">
          <i class="siteevent_icon_strip siteevent_icon siteevent_icon_tag" title="' . $view->translate("Category") . '"></i>
          <div class="o_hidden">'. 
            $siteevent->getCategory()->getTitle(true) .
            '</div>
        </div>';
        }

        if (in_array('ledBy', $eventInfo)) {
            $ledBys = $siteevent->getEventLedBys(in_array('hostName', $eventInfo));
            if (!empty($ledBys)) {
                echo '<div class="siteevent_listings_stats">
            <i class="siteevent_icon_strip siteevent_icon siteevent_icon_user" title="' . $view->translate("Leader") . '"></i>
            <div class="o_hidden">' .
                strip_tags($ledBys) .
                '</div>
          </div>';
            }
        }

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
            echo '<div class="siteevent_listings_stats">
        <i class="siteevent_icon_strip siteevent_icon siteevent_icon_stats" title="' . $view->translate("Statistics") . '"></i>
        <div class="o_hidden">' .
            $statistics .
            '</div>
      </div>';
        }

        if ((in_array('ratingStar', $eventInfo) && !empty($siteevent->review_count)) || (in_array('reviewCount', $eventInfo) && (!empty($siteevent->rating_editor) || !empty($siteevent->rating_users) || !empty($siteevent->$params['ratingValue']) ))) {
            if (in_array('ratingStar', $eventInfo) && in_array('reviewCount', $eventInfo))
                $iconHtmlTitle = $view->translate("Reviews & Ratings");
            else if (in_array('ratingStar', $eventInfo) && !in_array('reviewCount', $eventInfo))
                $iconHtmlTitle = $view->translate("Ratings");
            else if (!in_array('ratingStar', $eventInfo) && in_array('reviewCount', $eventInfo))
                $iconHtmlTitle = $view->translate("Reviews");

            echo '<div class="siteevent_listings_stats">
          <i class="siteevent_icon_strip siteevent_icon siteevent_icon_rating" title="' . $iconHtmlTitle . '"></i>
          <div class="o_hidden stats_rating_star">
            <div class="fleft">';
            if (in_array('ratingStar', $eventInfo)) {
                if ($params['ratingValue'] == 'rating_both') {
                    echo $view->ShowRatingStarSiteeventSM($siteevent->rating_editor, 'editor', $params['ratingShow']);
                    echo $view->ShowRatingStarSiteeventSM($siteevent->rating_users, 'user', $params['ratingShow']);
                }
                else
                    echo $view->ShowRatingStarSiteeventSM($siteevent->$params['ratingValue'], $params['ratingType'], $params['ratingShow']);
            }
            echo '</div>';
            if (in_array('reviewCount', $eventInfo)) {
                echo '<div class="fleft f_small"> ';
                echo $view->translate(array('%s review', '%s reviews', $siteevent->review_count), $view->locale()->toNumber($siteevent->review_count));
                echo '</div>';
            }
            echo '</div>
        </div>';
        }
    }

}